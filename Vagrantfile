# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

require 'yaml'
require 'fileutils'

config = {
  local: './vagrant/config/local.yml',
  example: './vagrant/config/local.yml.example'
}

# copy config from example if local config not exists
FileUtils.cp config[:example], config[:local] unless File.exist?(config[:local])
# read config
options = YAML.load_file config[:local]

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  
    config.vm.box = "ubuntu/xenial64"
    config.vm.box_check_update = options['box_check_update']

    config.vm.provider 'virtualbox' do |vb|
        vb.gui = options['gui']
        # machine cpus count
        vb.cpus = options['cpus']
        # machine memory size
        vb.memory = options['memory']
        # machine name (for VirtualBox UI)
        vb.name = options['machine_name']
    end

    # machine name (for vagrant console)
    config.vm.define options['machine_name']

    # machine name (for guest machine console)
    config.vm.hostname = options['machine_name']

    # network settings
    config.vm.network 'private_network', ip: options['ip']
    # config.vm.network "forwarded_port" , host: options['host_port'], guest: options['guest_port']

    config.vm.synced_folder "./", options['app_path'], id: "vagrant-root", :group=>'www-data', :mount_options=>['dmode=775,fmode=775']


    # disable folder '/vagrant' (guest machine)
    config.vm.synced_folder '.', '/vagrant', disabled: true

    # provisioners
    config.vm.provision 'shell', path: './vagrant/provision/once-as-root.sh', args: [options['app_path'], options['db_dev_root_pass'], options['db_dev_name'], options['db_dev_user'], options['db_dev_pass']]
    config.vm.provision 'shell', path: './vagrant/provision/once-as-vagrant.sh', args: [options['app_path']], privileged: false
    config.vm.provision 'shell', path: './vagrant/provision/once-as-root-test.sh', args: [options['app_path'], options['db_dev_root_pass'], options['db_test_name'], options['db_test_user'], options['db_test_pass']]
    config.vm.provision 'shell', path: './vagrant/provision/always-as-root-test.sh', args: [options['app_path']], run: 'always'
end