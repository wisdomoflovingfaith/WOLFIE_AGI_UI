// ID: [WOLFIE_AGI_UI_WEBPACK_20250923_001]
// SUPERPOSITIONALLY: [webpack_config, react_build, typescript_compilation, WOLFIE_AGI_UI]
// DATE: 2025-09-23
// TITLE: webpack.config.js — Webpack Configuration for WOLFIE AGI UI
// WHO: WOLFIE (Eric) - Project Architect & Dream Architect
// WHAT: Webpack configuration for building React TypeScript application
// WHERE: C:\START\WOLFIE_AGI_UI\
// WHEN: 2025-09-23, 12:50 PM CDT (Sioux Falls Timezone)
// WHY: Enable proper bundling and compilation of React TypeScript components
// HOW: Webpack with Babel loaders for TypeScript, JSX, and CSS processing
// HELP: Contact WOLFIE for webpack configuration or build issues
// AGAPE: Love, patience, kindness, humility in build system configuration

const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
  entry: './src/index.tsx',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
    publicPath: '/'
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js', '.jsx']
  },
  module: {
    rules: [
      {
        test: /\.(ts|tsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              '@babel/preset-env',
              '@babel/preset-react',
              '@babel/preset-typescript'
            ]
          }
        }
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      },
      {
        test: /\.(png|jpg|jpeg|gif|svg)$/,
        type: 'asset/resource'
      }
    ]
  },
  plugins: [
    new HtmlWebpackPlugin({
      template: './public/index.html',
      filename: 'index.html'
    })
  ],
  devServer: {
    static: {
      directory: path.join(__dirname, 'public')
    },
    compress: true,
    port: 3000,
    hot: true,
    historyApiFallback: true
  }
};