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
                        if (!fileExists('composer.phar')) {
                            sh 'curl -sS https://getcomposer.org/installer | php'
                        }
                        sh 'php composer.phar install'
                        sh 'mkdir -p build/logs'
                        sh 'vendor/bin/phpunit --log-junit build/logs/junit.xml || true'
                    }
                }
            }
            post {
                always {
                    junit '**/build/logs/junit.xml'
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