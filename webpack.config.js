const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const BundleAnalyzerPlugin = require("webpack-bundle-analyzer").BundleAnalyzerPlugin;
const CopyWebpackPlugin = require("copy-webpack-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const SVGSpritemapPlugin = require("svg-spritemap-webpack-plugin");
const WebpackAssetsManifest = require("webpack-assets-manifest");
const { merge } = require("webpack-merge");
const path = require("path");
const glob = require("glob");

const DEV_MODE = process.env.NODE_ENV !== "production";
const PROD_MODE = !DEV_MODE;
const USE_ANALYZE = process.env.ANALYZE !== undefined;
const USE_SOURCEMAPS = process.env.SOURCEMAPS !== undefined;
const THEME_PATH = "themes/app";

const PATHS = {
  SRC: path.resolve(THEME_PATH, "src"),
  DIST: path.resolve(THEME_PATH, "dist"),
  TEMPLATES: path.resolve(__dirname, `${THEME_PATH}/templates`),
  PUBLIC: `/_resources/${THEME_PATH}/dist/`,
  MODULES: path.resolve(__dirname, "node_modules"),
};

const generateEntries = (base) => {
  return glob.sync(path.join(PATHS.SRC, `${base}/*`)).reduce((acc, filepath) => {
    const name = path.parse(filepath).name;
    const key = `${base}-${name}`;

    if (key in acc) {
      acc[key].push(filepath);
    } else {
      acc[key] = [filepath];
    }

    return acc;
  }, {});
};

const WebpackConfig = {
  mode: process.env.NODE_ENV,

  entry: {
    app: path.join(PATHS.SRC, "index.js"),
    ...generateEntries("elements"),
    ...generateEntries("components"),
    ...generateEntries("pages"),
  },

  output: {
    filename: "[name].js",
    path: PATHS.DIST,
    publicPath: PATHS.PUBLIC,
  },

  resolve: {
    extensions: [".js"],
    alias: {
      "@scripts": path.resolve(PATHS.SRC, "scripts"),
      "@common": path.resolve(PATHS.SRC, "scripts/common"),
      "@react": path.resolve(PATHS.SRC, "scripts/react"),
      "@components": path.resolve(PATHS.SRC, "scripts/components"),
      "@elements": path.resolve(PATHS.SRC, "scripts/elements"),
      "@pages": path.resolve(PATHS.SRC, "scripts/pages"),
      "@styles": path.resolve(PATHS.SRC, "styles"),
      "@images": path.resolve(PATHS.SRC, "images"),
      "@fonts": path.resolve(PATHS.SRC, "fonts"),
    },
  },

  module: {
    rules: [
      {
        test: /.(js)$/,
        exclude: [PATHS.MODULES],
        use: [
          {
            loader: "babel-loader",
          },
        ],
      },
      {
        test: /\.css$/,
        use: [
          DEV_MODE ? "style-loader" : MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              sourceMap: PROD_MODE || USE_SOURCEMAPS,
            },
          },
          {
            loader: "postcss-loader",
            options: {
              sourceMap: PROD_MODE || USE_SOURCEMAPS,
            },
          },
        ],
      },
      {
        test: /\.(jpe?g|svg|png|gif|ico|eot|ttf|woff2?)(\?v=\d+\.\d+\.\d+)?$/i,
        type: "asset/resource",
      },
    ],
  },

  optimization: {
    runtimeChunk: 'single',
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        polyfills: {
          test: /[\\/]node_modules[\\/](@babel|core-js|regenerator-runtime)[\\/]/,
          name: 'polyfills',
          chunks: 'initial',
          priority: 60,
          enforce: true,
          reuseExistingChunk: true,
        },
      },
    },
  },

  plugins: [
    new CleanWebpackPlugin({
      cleanStaleWebpackAssets: false,
      cleanAfterEveryBuildPatterns: ["*hot-update*"],
    }),
    new CopyWebpackPlugin({
      patterns: [
        {
          from: "images",
          to: "images/",
          context: PATHS.SRC,
          noErrorOnMissing: true,
          globOptions: {
            ignore: ["**/.gitkeep"],
          },
        },
        {
          from: "favicon",
          to: "favicon/",
          context: PATHS.SRC,
          noErrorOnMissing: true,
          globOptions: {
            ignore: ["**/.gitkeep"],
          },
        },
        {
          from: "svg",
          to: "svg/",
          context: PATHS.SRC,
          noErrorOnMissing: true,
          globOptions: {
            ignore: ["**/.gitkeep"],
          },
        },
      ],
    }),
    new SVGSpritemapPlugin(path.join(PATHS.SRC, "svg/*.svg"), {
      output: {
        svgo: {
          plugins: ["removeTitle", { name: "convertColors", params: { currentColor: true } }],
        },
      },
    }),
    new MiniCssExtractPlugin({
      filename: "[name].css",
    }),
    new WebpackAssetsManifest({
      entrypoints: true,
      writeToDisk: true,
    }),
  ],
};

if (USE_ANALYZE) {
  WebpackConfig.plugins.push(
    new BundleAnalyzerPlugin({
      analyzerMode: "server",
      analyzerHost: process.env.ANALYZER_HOST || "127.0.0.1",
    }),
  );
}

if (DEV_MODE) {
  module.exports = merge(WebpackConfig, {
    devtool: "inline-cheap-module-source-map",
    devServer: {
      allowedHosts: "all",
      client: {
        overlay: {
          errors: true,
          warnings: false,
        },
        webSocketURL: process.env.WEB_SOCKET_URL || "ws://localhost:8080/ws",
      },
      devMiddleware: {
        writeToDisk: true,
      },
      watchFiles: {
        paths: [path.join(`${PATHS.TEMPLATES}/**/*.ss`)],
      },
    },
    cache: {
      type: "filesystem",
    },
  });
}

if (PROD_MODE) {
  module.exports = merge(WebpackConfig, {
    devtool: "source-map",
    optimization: {
      minimize: true,
      minimizer: ["...", new CssMinimizerPlugin()],
    },
  });
}
