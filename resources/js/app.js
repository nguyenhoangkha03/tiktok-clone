import './bootstrap';

import Alpine from 'alpinejs';

// Import Phase 6 modules
import initVideoPlayer, { volumeControl, progressBar } from './video-player';
import initScrollHandler from './scroll-handler';
import initInteractions, { videoActions, commentSection } from './interactions';

// Register Alpine components
Alpine.data('volumeControl', volumeControl);
Alpine.data('progressBar', progressBar);
Alpine.data('videoActions', videoActions);
Alpine.data('commentSection', commentSection);

window.Alpine = Alpine;

Alpine.start();

// Initialize Phase 6 features when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initVideoPlayer();
    initScrollHandler();
    initInteractions();
});

// Expose for re-initialization after AJAX loads new videos
window.initVideoPlayer = initVideoPlayer;
