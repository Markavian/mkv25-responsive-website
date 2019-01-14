const ftpDeploy = require('ftp-deploy')
const deployer = new ftpDeploy()
const fs = require('fs')
const path = require('path')

const passwords = JSON.parse(fs.readFileSync(path.join(__dirname, '.ftppass')))
const mode = process.argv[2] || false

const defaultConfig = {
  user: passwords['mkv25-stage'].username,
  password: passwords['mkv25-stage'].password, // optional, prompted if none given
  host: 'ftp.mkv25.net',
  port: 21,
  localRoot: path.join(__dirname, '../'),
  remoteRoot: '/',
  // include: ['*', '**/*'],      // this would upload everything except dot files
  include: [], // ['*.php', 'dist/*'],
  exclude: ['.ftp*', '.git*', '.hta*', 'deploy', '*.fdproj', 'tasklist.md', 'readme.md', '.sublime'], // ['dist/**/*.map'], // e.g. exclude sourcemaps - ** exclude: [] if nothing to exclude **
  deleteRemote: false, // delete existing files at destination before uploading
  forcePasv: true // Passive mode is forced (EPSV command is not sent)
}

const variations = {
  'stage-images': {
    include: ['site/images/**/*']
  },
  'stage-code': {},
  'stage-all': {
    include: ['site/**/*']
  },
  'stage-php': {},
  'stage-php-lib': {},
  'stage-scripts': {},
  'stage-stylesheets': {},
  'stage-templates': {},
  'stage-content': {},
  'stage-articles': {},
  'stage-projects': {},
  'live-release': {}
}

deployer.on('uploading', function(data) {
  console.log(data)
  console.log('[Deploy] Uploading:', data.filename, ',', data.transferredFileCount, 'of', data.totalFilesCount, 'complete')
})

deployer.on('uploaded', function(data) {
  console.log(data)
  console.log('[Deploy] Uploaded:', data.filename, ',', data.transferredFileCount, 'of', data.totalFilesCount, 'complete')
})

deployer.on('log', function(data) {
  console.log('[Deploy]', data)
})

async function deploy(mode) {
  const variation = variations[mode] || false
  if (variation) {
    const ftpConfig = Object.assign({}, defaultConfig, variation)
    try {
      console.log('[Deploy]', mode, ':', variation)
      const result = await deployer.deploy(ftpConfig)
      console.log('[Deploy] Finished:', result)
    }
    catch (ex) {
      console.log('[Deploy] Error:', ex)
    }
  } else {
    console.log(`[Deploy] No variation found for mode (${mode})`)
  }
}

deploy(mode)
