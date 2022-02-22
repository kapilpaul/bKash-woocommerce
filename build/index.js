const fs = require('fs');
const path = require('path');
const minimist = require('minimist');
const shell = require('shelljs');

function resolve(...paths) {
	return path.resolve(__dirname, ...paths);
}

const DEST = resolve('woo-payment-bkash');
const packageInfo = JSON.parse(fs.readFileSync('package.json'));
const args = minimist(process.argv.slice(2));

let version = packageInfo.version;

const semverRegex = /^((([0-9]+)\.([0-9]+)\.([0-9]+)(?:-([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?)(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?)$/;

if (args.version && args.version.match(semverRegex)) {
	const currentVersion = version;
	version = args.version;

	console.log('Updating plugin version number');

	shell.exec(
		`sed -i '' 's/"version": "${currentVersion}"/"version": "${version}"/g' package.json`
	);
	shell.exec(
		`sed -i '' 's/* Version: ${currentVersion}/* Version: ${version}/g' woo-payment-bkash.php`
	);
	shell.exec(
		`find includes -iname '*.php' -exec sed -i "" "s/DC_BKASH_SINCE/${version}/g" {} \\\;`
	);
	shell.exec(`npm install`);
}

console.log('Installing composer without dev dependencies...');

shell.exec(`composer install --optimize-autoloader --no-dev`);

const zip = `woo-payment-bkash-${version}.zip`;

shell.rm('-rf', DEST);
shell.rm('-f', resolve('woo-payment-bkash-*.zip'));
shell.mkdir('-p', DEST + '/assets');

const include = [
	'assets/css',
	'assets/images',
	'assets/js',
	'includes',
	'languages',
	'templates',
	'vendor',
	'composer.json',
	'index.php',
	'woo-payment-bkash.php',
	'README.md',
	'readme.txt',
];

console.log('Copying files...');

include.forEach((item) => {
	shell.cp('-r', resolve('../', item), resolve(DEST, item));
});

console.log('Making zip...');
shell.exec(`cd ${resolve()} && zip ${zip} woo-payment-bkash -rq`);

shell.rm('-rf', resolve(DEST));
console.log('Done.');
