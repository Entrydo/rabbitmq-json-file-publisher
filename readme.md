# Import to rabbit tool

## Purpose
Publish data json file into RabbitMQ 

## Install
1. Create `.env` file (use `.env.example` as template) 
2. Install composer using `composer install`

## Usage
- `php src/script.php <pathToJsonFile> <rabbitQueueName> <rabbitExchangeName>`

## TODO
- [ ] Queue name as parameter
- [ ] Dockerfile
- [ ] Phpstan
- [ ] Coding standard
- [ ] CI/CD ?
- [ ] Composer package? -> would allow to be used in projects and/or separated as binary only
- [ ] [CLImate](http://climate.thephpleague.com)? 
    - [ ] `--help` ?
