import { sendToAnalytics } from './send-to-analytics';
import { onCLS, onFCP, onLCP, onTTFB, onINP } from 'web-vitals/attribution';

export function measureWebVitals() {
	onCLS( sendToAnalytics );
	onFCP( sendToAnalytics );
	onLCP( sendToAnalytics );
	onTTFB( sendToAnalytics );
	onINP( sendToAnalytics );
}
