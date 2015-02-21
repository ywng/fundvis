git add *

read -p "Default commit message? y/n " default
if [ "$default" == "Y" ] || [ "$default" == "y" ]; then
	message="default: no special comment"
else
	read -p "Please input your commit message: " message
fi

git commit -a -m "$message"
git push openshift master
git push origin master
