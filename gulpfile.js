const gulp = require( 'gulp' );
const zip = require( 'gulp-zip' );
const rename = require( 'gulp-rename' );
const path = require( 'path' );

function zipBundle() {
	const bundleDirName = 'site-performance-tracker';

	return gulp
		.src(
			[
				'*.php',
				'README.md',
				'css/**/*.css',
				'js/dist/**/*.js',
				'php/**/*.php',
			],
			{
				base: __dirname, // Resolve file paths relative to the plugin root directory.
			}
		)
		.pipe(
			rename( ( filePath ) => {
				filePath.dirname = path.join( bundleDirName, filePath.dirname ); // Move files into a "fake" directory.
				console.log( // eslint-disable-line no-console
					`Adding: ${ filePath.dirname }/${ filePath.basename }${ filePath.extname }`
				);
				return filePath;
			} )
		)
		.pipe( zip( `${ bundleDirName }.zip` ) )
		.pipe( gulp.dest( __dirname ) );
}

exports.zip = zipBundle;
