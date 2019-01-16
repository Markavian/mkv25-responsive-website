# mkv25.net deployment system

This grunt plugin manages the release and deployment of the main mkv25.net website, managing stage and live releases. It contains a scripts to automate the release procedure, including:

- Uploading files via FTP to a web server

## Setup

This deployment system is designed to work with a specific folder structure, with the site living in a folder in the directory below the plugin.

To get going:

- Copy and rename .ftppass-template to .ftppass
- Populate .ftppass with FTP Username and Password details

Then run: `npm install`

Provided the FTP details are correct, you can then run the stage and live release commands:
- npm run stage-code
- npm run stage-images
- npm run stage-content
- npm run stage-articles
- npm run stage-all
- npm run live-release

## Configuration

Other variables you might want to change, include:

deploy.js:

```javasrcipt
    const FTP_HOST = 'ftp.mkv25.net'
    const FTP_USER_STAGE = 'mkv25-stage'
    const FTP_USER_LIVE = 'mkv25-live'
    const FTP_ROOT_PATH = ''
```

## Wishlist

All peachy at the moment
