
## Install

```
git clone git@bitbucket.org:edgedesigncz/qa-cli.git
composer install
chmod 755 phpqa
    # otherwise: -bash: /usr/bin/phpqa: Permission denied
pwd
    # /home/jenkins/jenkins-jobs/qa-cli
sudo ln -s /home/jenkins/jenkins-jobs/qa-cli/phpqa /usr/bin/phpqa
```

## Run

```
phpqa --analyzedDir=./ --buildDir=build/ --ignoredDirs build,vendor --ignoredFiles=RoboFile.php
```

## Phing

### Source directory with tests inside bundles

```
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDir=./src" />
        <arg value="--buildDir=./build" />
        <arg value="--ignoredDirs= " />
        <arg value="--ignoredFiles= " />
    </exec>
</target>
```

### Separated source and test directories

```
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDir=./" />
        <arg value="--buildDir=./build" />
        <arg value="--ignoredDirs=app,build,features,vendor,web" />
        <arg value="--ignoredFiles= " />
    </exec>
</target>
```