import { measureWebVitals } from './web-vitals-analytics.js';

test( 'can call measureWebVitals()', () => {
	expect( typeof measureWebVitals ).toBe( 'function' );
} );
