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
- stage-code
- stage-images
- stage-all
- live-release

## Configuration

Other variables you might want to change, include:

gruntfile.js:

```javasrcipt
    var FTP_HOST = 'ftp.mkv25.net';
    var FTP_USER_STAGE = 'mkv25-stage';
    var FTP_USER_LIVE = 'mkv25-live';
    var FTP_PATH = '';
```

## Wishlist

All peachy at the moment
