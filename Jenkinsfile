pipeline {
    agent { docker { image 'php:7.4-apache' } }
    stages {
        stage('build') {
            steps {
                sh 'php --version'
            }
        }
    }
}
