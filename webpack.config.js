const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'development',
  entry: {
    main: './src/timqwees_scripts/script/index.js',
    style: './src/timqwees_scripts/style/style.css'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'assets/distJS')
  },
  experiments: {
    outputModule: true
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        type: 'javascript/auto'
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader']
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '../distCSS/[name].css'
    })
  ]
};
