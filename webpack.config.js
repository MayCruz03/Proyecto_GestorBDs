const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    entry: {
        menuBar: './src/js/menuBar.js',
        // archivo1: './js/archivo1.js', // Otros archivos de entrada
        // archivo2: './js/archivo2.js',
    },
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'dist') // Carpeta de salida
    },
    mode: 'production', // Modo producción para minificación
    optimization: {
        minimize: true,
        minimizer: [new TerserPlugin()],
    },
    watch: true // Observa los cambios en los archivos
};