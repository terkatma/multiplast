#!/usr/bin/env bash
echo -e "Check if command \`curl\` is executable: ";
if ! [ -x "$(command -v curl)" ]; then
  echo -e "\nCommand \`curl\` is not executable or \`curl\` is not installed.\n";
  echo -e "\nInstalling curl.\n";
  sudo apt install curl;
else
	echo -e "Ok.\n";
fi
echo -e "Check if command \`php\` is executable: ";
if ! [ -x "$(command -v php)" ]; then
  echo -e "\nCommand \`php\` is not executable or \`php\` is not installed.\n";
  echo -e "\nInstalling php.\n";
  sudo apt install php5.0-cli;
else
	echo -e "Ok.\n";
fi
echo -e "Check if command \`composer\` is executable: ";
if ! [ -x "$(command -v composer)" ]; then
  echo -e "\nCommand \`composer\` is not executable or \`composer\` is not installed.\n";
  echo -e "\nInstalling composer.\n";
  curl -s http://getcomposer.org/installer | php;
  mv ./composer.phar /usr/local/bin/composer
else
	echo -e "Ok.\n";
fi

echo -e "Check if command \`bower\` is executable: ";
if ! [ -x "$(command -v bower)" ]; then
  echo -e "\nCommand \`bower\` is not executable or \`bower\` is not installed.\n";
  echo -e "\nInstalling bower.\n";
  sudo apt install bower;
else
	echo -e "Ok.\n";
fi

sudo apt-get install php-mbstring
sudo apt-get install php-curl
sudo apt-get install php-dom
sudo apt-get install php-mysql
sudo apt-get install php-zip