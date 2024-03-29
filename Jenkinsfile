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
        stage('PHP Unit Tests') {
            steps {
                script {
                    dir('src/PhpWebApp') {
                        if (!fileExists('vendor')) {
                            sh 'composer install'
                        }
                        sh 'mkdir -p build/logs'
                        sh 'vendor/bin/phpunit --log-junit build/logs/junit.xml'
                    }
                }
            }
            post {
                always {
                    junit 'src/PhpWebApp/build/logs/junit.xml'
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