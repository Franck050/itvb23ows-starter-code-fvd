pipeline {
    agent any
    stages {
        stage('SonarQube Analysis') {
            steps {
                script { scannerHome = tool 'OWS-SonarQube-Scanner' }
                withSonarQubeEnv('OWS_Sonar') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=ows"
                }
            }
        }
        stage('Docker compose up') {
            steps {
                script {
                    sh 'echo "Building..."'
                    sh 'docker compose up --build -d'
                }
            }
        }
        stage('PHP Unit Test') {
            steps {
                dir("src/PhpWebApp") {
                    sh 'echo "Testing..."'
                    sh ' ./vendor/bin/phpunit tests'
                }
            }
        }
    }
    post {
        always {
            sh 'docker compose down'
        }
    }
}