import { createApp } from 'vue'
import BVNodeSets from './BVNodeSets.vue'

let sets = document.querySelector('[data-sets]');
window.setsApp = createApp(BVNodeSets, {
    "config": sets.getAttribute('data-config'),
    "setsValue": document.querySelector('textarea').value

}).mount(sets);


