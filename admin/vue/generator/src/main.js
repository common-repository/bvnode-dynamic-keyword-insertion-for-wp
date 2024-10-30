import { createApp } from 'vue'
import BVNodeGenerator from './BVNodeGenerator.vue'

let generator = document.querySelector('[data-generator]');
window.generatorApp = createApp(BVNodeGenerator, {}).mount(generator);


