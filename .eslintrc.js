module.exports = {
  root: true,
  extends: ["eslint:recommended", "prettier"],
  env: {
    browser: true,
    es6: true,
    node: true,
  },
  ignorePatterns: ["node_modules"],
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    sourceType: "module",
    ecmaVersion: 2020,
  },
  rules: {
    "no-console": 1,
  },
};
