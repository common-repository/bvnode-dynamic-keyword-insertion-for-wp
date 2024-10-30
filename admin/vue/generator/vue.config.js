const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  productionSourceMap: false,
  configureWebpack: config => { 
    if(process.env.NODE_ENV === "production") {
      config.output.filename = 'js/generator.[name].min.js'
      config.output.chunkFilename = 'js/generator.[name].min.js'
    } else {
      config.output.filename = 'js/[name].js'
      config.output.chunkFilename = 'js/[name].js';
    }
  },
  css: {
    extract: { 
      ignoreOrder: true,
      filename: 'css/generator.[name].min.css',
      chunkFilename: 'css/generator.[name].min.css', 
    },
  }
})
