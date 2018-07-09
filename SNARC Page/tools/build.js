{
    appDir: '../',
    baseUrl: 'js',
    paths: {
        jQuery: "empty:",
        domReady : 'require-plugins/domReady'
    },
    dir: 'snarc',
    modules: [
    {
        name: 'main',
        include: [ "jQuery","domReady","lib/modal","modules/bootstrap","lib/yummy","lib/pace","lib/nicescroll" ]
  	}]
}
