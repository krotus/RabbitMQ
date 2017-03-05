Vagrant.configure("2") do |config|

    config.vm.box = "gbarbieru/xenial"
    config.vm.hostname = "symfony-rabbit-model"
    config.vm.provision "shell", path: "vagrant/bootstrap_root.sh"
    config.vm.provision "shell", path: "vagrant/bootstrap_user.sh", privileged: false

end