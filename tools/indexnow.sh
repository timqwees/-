#!/bin/bash
# IndexNow submission script for фсс.рус
# Usage: ./tools/indexnow.sh

set -euo pipefail

HOST="фсс.рус"
# Generate new random key if not provided
KEY="${INDEXNOW_KEY:-$(php -r 'echo bin2hex(random_bytes(16));')}"

echo "=== IndexNow Submission for $HOST ==="
echo "Key: $KEY"
echo ""

# Ensure key file exists in project root (accessible at /{KEY}.txt)
ROOT_KEY_FILE="${KEY}.txt"
if [[ ! -f "$ROOT_KEY_FILE" ]]; then
    echo "Creating key file in project root: $ROOT_KEY_FILE"
    echo "$KEY" > "$ROOT_KEY_FILE"
fi

# Also ensure key file in public/ (for direct access)
PUBLIC_KEY_FILE="public/${KEY}.txt"
if [[ ! -f "$PUBLIC_KEY_FILE" ]]; then
    echo "Creating key file in public/: $PUBLIC_KEY_FILE"
    echo "$KEY" > "$PUBLIC_KEY_FILE"
fi

echo "Key file must be accessible at: https://$HOST/$KEY.txt"
echo ""

# Collect all URLs
URLS=()

# Main pages
URLS+=(
  "https://$HOST/"
  "https://$HOST/uslugi"
  "https://$HOST/portfolio"
  "https://$HOST/about"
  "https://$HOST/company"
  "https://$HOST/contacts"
  "https://$HOST/blog"
  "https://$HOST/politika"
  "https://$HOST/soglasie"
)

# Service pages (from public/pages/services/*/index.php)
for service_dir in public/pages/services/*/; do
    if [[ -d "$service_dir" && -f "${service_dir}index.php" ]]; then
        service=$(basename "$service_dir")
        URLS+=("https://$HOST/services/$service")
    fi
done

# Geo landing pages (from app/Data/geo.php)
if [[ -f "app/Data/geo.php" ]]; then
    echo "Loading geo data..."
    geo_services=$(php -r '
        $geo = require "app/Data/geo.php";
        $priority = $geo["priority_services"] ?? [];
        $geo_list = $geo["geo"] ?? [];
        foreach ($priority as $svc) {
            foreach (array_keys($geo_list) as $g) {
                echo "services/$svc/$g\n";
            }
        }
    ')
    for path in $geo_services; do
        URLS+=("https://$HOST/$path")
    done
fi

# Blog pages (from app/Data/blog.php)
if [[ -f "app/Data/blog.php" ]]; then
    echo "Loading blog posts..."
    blog_slugs=$(php -r '
        $blog = require "app/Data/blog.php";
        foreach (array_keys($blog) as $slug) {
            echo "blog/$slug\n";
        }
    ')
    for slug in $blog_slugs; do
        URLS+=("https://$HOST/$slug")
    done
fi

# Portfolio pages (from app/Data/projects.php)
if [[ -f "app/Data/projects.php" ]]; then
    echo "Loading portfolio projects..."
    project_slugs=$(php -r '
        $projects = require "app/Data/projects.php";
        foreach (array_keys($projects) as $slug) {
            echo "portfolio/$slug\n";
        }
    ')
    for slug in $project_slugs; do
        URLS+=("https://$HOST/$slug")
    done
fi

# Technical files
URLS+=(
  "https://$HOST/sitemap.xml"
  "https://$HOST/sitemap-index.xml"
  "https://$HOST/robots.txt"
  "https://$HOST/llms.txt"
  "https://$HOST/llms-full.txt"
  "https://$HOST/manifest.json"
  "https://$HOST/opensearch.xml"
)

# Deduplicate
URLS=($(printf '%s\n' "${URLS[@]}" | sort -u))

echo "Total unique URLs: ${#URLS[@]}"
echo ""

# Build JSON payload using Python (handles JSON encoding properly)
PAYLOAD=$(python3 -c "
import json, sys
host = sys.argv[1]
key = sys.argv[2]
urls = sys.argv[3:]
payload = {
    'host': host,
    'key': key,
    'keyLocation': f'https://{host}/{key}.txt',
    'urlList': urls
}
print(json.dumps(payload, ensure_ascii=False))
" "$HOST" "$KEY" "${URLS[@]}")

# Submit to IndexNow endpoints
ENDPOINTS=(
  "https://www.bing.com/indexnow"
  "https://yandex.com/indexnow"
)

for endpoint in "${ENDPOINTS[@]}"; do
  echo -n "Submitting to $endpoint ... "
  code=$(curl -s -X POST "$endpoint" \
    -H "Content-Type: application/json; charset=utf-8" \
    -d "$PAYLOAD" \
    -o /dev/null \
    -w "%{http_code}" \
    --max-time 60)
  echo "HTTP $code"
  if [[ "$code" != "200" && "$code" != "202" ]]; then
    echo "  Warning: Non-200/202 response"
  fi
done

echo ""
echo "=== Done ==="
echo "Key: $KEY"
echo "Key file must be accessible at: https://$HOST/$KEY.txt"
echo "Run after deploying to production."