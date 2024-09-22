import './bootstrap';
import Chart from 'chart.js/auto';
import grapesjs from 'grapesjs';

document.addEventListener('DOMContentLoaded', function () {
    const editor = grapesjs.init({
        container: '#gjs', // Ini ID dari elemen di mana editor akan ditampilkan
        height: '100%',
        fromElement: true,
        storageManager: false,
        panels: { defaults: [] },
        blockManager: {
            appendTo: '#blocks', // ID untuk panel block manager
        },
    });
});
