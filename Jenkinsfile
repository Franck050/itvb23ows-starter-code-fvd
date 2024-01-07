pipeline {
    agent any
    stages {
        stage('build') {
            agent {
                docker {
                    image 'php:7.4-apache'
                    }
                }
            steps {
                sh 'php --version'
            }
        }
    }
}
