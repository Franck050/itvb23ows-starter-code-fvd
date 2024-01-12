pipeline {
    agent any
    stages {
        stage('build') {
            agent { docker { image 'php:8.3.0-alpine3.19' } }
            steps {
                sh 'php --version'
            }
        }
        stage('SonarQube Analysis') {
            steps {
                script { scannerHome = tool 'OWS-SonarQube-Scanner' }
                withSonarQubeEnv('OWS_Sonar') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=[key]"
                }

            }
        }
    }
}



