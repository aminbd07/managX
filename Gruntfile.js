module.exports = function(grunt) {
    var pkg = grunt.file.readJSON('package.json');

    grunt.initConfig({
        // setting folder templates
        dirs: {
            css: 'assets/css',
            images: 'assets/images',
            js: 'assets/js'
        },

        less: {
            development: {
                options: {
                    compress: false,
                    yuicompress: false,
                    optimization: 2
                },
                files: {
                  
                    "assets/css/style.css": "assets/css/less/style.less", // destination file and source file
                    
                }
            }
        },
         browserify: {
            dist: {
                options: {
                    transform: [['partialify']]
                },
                files: { 
                    "./assets/js/managx-script.js": ["./assets/js/raw/managex-script.js"] // For Taks List and Task
                }
            }
        },
        watch: {
            styles: {
                files: ['assets/css/less/*.less', 'assets/js/raw/*.js'], // which files to watch
                tasks: ['less', 'browserify'],
                options: {
                    nospawn: true
                }
            }
        },

        // Clean up build directory
        clean: {
            main: ['build/']
        },

        // Copy the plugin into the build directory
        copy: {
            main: {
                src: [
                    '**',
                    '!node_modules/**',
                    '!build/**',
                    '!bin/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!debug.log',
                    '!phpunit.xml',
                    '!export.sh',
                    '!.gitignore',
                    '!.gitmodules',
                    '!npm-debug.log',
                    '!plugin-deploy.sh',
                    '!readme.md',
                    '!composer.json',
                    '!secret.json',
                    '!assets/less/**',
                    '!tests/**',
                    '!**/Gruntfile.js',
                    '!**/package.json',
                    '!**/README.md',
                    '!nbproject',
                    '!**/*~'
                ],
                dest: 'build/'
            }
        },

        concat: {
            '<%= dirs.js %>/managex-all.js': [
                '<%= dirs.js %>/managex-script.js',
               
            ]
        },

        //Compress build directory into <name>.zip and <name>-<version>.zip
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './build/managx-' + pkg.version + '.zip'
                },
                expand: true,
                cwd: 'build/',
                src: ['**/*'],
                dest: 'managx'
            }
        },

        replace: {
            example: {
                src: ['build/managx.php'],
                dest: 'build/managx.php',
                replacements: [
                    {
                        from: 'ManageX Project Manager',
                        to: 'ManagX Project Manager Premimum'
                    },
                    {
                        from: 'https://wordpress.org/plugins/managx/',
                        to: ''
                    }
                ]
            }
        },

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*'],
                    domainPath: '/lang/', // Where to save the POT file.
                    potFilename: 'managx.pot', // Name of the POT file.
                    type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
                    potHeaders: {
                        'report-msgid-bugs-to': 'http://managx.com/support/',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
                    }
                }
            }
        },

        secret: grunt.file.readJSON('secret.json'),
        sshconfig: {
            "myhost": {
                host: '<%= secret.host %>',
                username: '<%= secret.username %>',
                password : "vagrant",
                agent: process.env.SSH_AUTH_SOCK,
                agentForward: true
            }
        },
        sftp: {
            upload: {
                files: {
                    "./": 'build/managx-premium-v' + pkg.version + '.zip'
                },
                options: {
                    path: '<%= secret.path %>',
                    config: 'myhost',
                    showProgress: true,
                    srcBasePath: "build/"
                }
            }
        },
        sshexec: {
            updateVersion: {
                command: '<%= secret.updateFiles %> ' + pkg.version + ' --allow-root',
                options: {
                    config: 'myhost'
                }
            },

            uptime: {
                command: 'uptime',
                options: {
                    config: 'myhost'
                }
            },

             
        }
    });

    grunt.loadNpmTasks( 'grunt-contrib-less' );
    grunt.loadNpmTasks( 'grunt-contrib-concat' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    // grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-ssh' );
    grunt.loadNpmTasks( 'grunt-browserify' );
    grunt.registerTask('default', ['less', 'watch']);


    grunt.registerTask( 'build', [ 'browserify' ] );

    grunt.registerTask('release', [
        'makepot',
        'less',
        'concat',
        // 'clean',
        // 'copy',
        // 'compress'
        // 'uglify'
    ]);

    grunt.registerTask( 'zip', [
        'clean',
        'copy',
        'replace',
        'compress'
    ])

    grunt.registerTask( 'deploy', [
        'sftp:upload', 'sshexec:updateVersion'
    ]);
};