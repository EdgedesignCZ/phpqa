
## Install

```
git clone git@bitbucket.org:edgedesigncz/qa-cli.git
composer install
chmod 755 bin/phpqa
    # otherwise: -bash: /usr/bin/phpqa: Permission denied
pwd
    # /home/jenkins/jenkins-jobs/qa-cli
sudo ln -s /home/jenkins/jenkins-jobs/qa-cli/bin/phpqa /usr/bin/phpqa
```

## Run

```
bin/robo ci --analyzedDir=./ --buildDir=build/ --ignoredDirs build,vendor --ignoredFiles=RoboFile.php
```

## Phing

```
<target name="ci-phpqa">
    <exec executable="phpqa.php" passthru="true">
        <arg value="--analyzedDir='${application.startdir}/src'" />
        <arg value="--buildDir='${application.startdir}/build/logs'" />
    </exec>
</target>
```