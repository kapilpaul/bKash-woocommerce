// eslint.config.cjs
const js = require("@eslint/js");

module.exports = [
	// top-level ignore patterns (replaces .eslintignore)
	{
		ignores: [
			"**/*.min.js",
			"**/node_modules/**",
			"**/vendor/**",
			"**/build/**",
			".github",
			"assets/js/app.js",
			"assets/js/runtime.js",
			"assets/js/upgrade.js",
			"assets/js/vendors.js",
			"assets/src/admin/Pages/Doc/**",
			"assets/src/admin/Pages/generatedoc.js",
		],
	},

	// base JS config
	js.configs.recommended,

	// general rules and JSX support for JS/JSX files
	{
		files: ["**/*.{js,jsx}"],
		plugins: {
			react: require("eslint-plugin-react"),
		},
		languageOptions: {
			ecmaVersion: "latest",
			sourceType: "module",
			parserOptions: {
				ecmaVersion: "latest",
				sourceType: "module",
				ecmaFeatures: { jsx: true },
			},

			// ✅ Explicitly declare browser & runtime globals here (flat-config replacement for "env: { browser: true }")
			globals: {
				// browser timers & window / document
				setTimeout: true,
				clearTimeout: true,
				setInterval: true,
				clearInterval: true,
				requestAnimationFrame: true,
				cancelAnimationFrame: true,

				window: true,
				document: true,
				navigator: true,
				location: true,
				localStorage: true,
				sessionStorage: true,
				fetch: true,
				Headers: true,
				Request: true,
				Response: true,
				FormData: true,
				URL: true,
				URLSearchParams: true,
				performance: true,
				Event: true,
				CustomEvent: true,

				// Node-ish globals you might want (optional — remove if not needed)
				// process: true,

				// Your plugin-specific global
				dc_bkash_admin: true,
				bKash: true,
				jQuery: true,
			},
		},
		settings: {
			react: { version: "detect" },
		},
		rules: {
			// React 17+ JSX transform fixes
			"react/react-in-jsx-scope": "off",
			"react/jsx-uses-react": "off",

			// Ensure variables referenced in JSX count as "used"
			"react/jsx-uses-vars": "error",

			// keep React import safe but don't ignore other unused vars
			"no-unused-vars": [
				"error",
				{
					varsIgnorePattern: "^React$",
					args: "none",
				},
			],
		},
	},

	// optional: separate override just for .jsx files (if you want stricter React rules)
	{
		files: ["**/*.jsx"],
		languageOptions: {
			parserOptions: { ecmaFeatures: { jsx: true } },
		},
	},
];
