pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'composer install'
      }
    }
    stage('Test') {
      steps {
        sh './vendor/bin/phpunit'
      }
    }
    stage('Pack it up') {
      steps {
        archiveArtifacts(allowEmptyArchive: true, fingerprint: true, onlyIfSuccessful: true, artifacts: '*')
      }
    }
    stage('Deploy') {
      steps {
        echo 'Done'
      }
    }
  }
}