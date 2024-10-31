rsync -av --exclude=".*" --exclude="node_modules" ./ ~/Work/yme/svn/protect-pages-posts/trunk/
cd ~/Work/yme/svn/protect-pages-posts/trunk
svn add --force .
svn commit -m "bump new version"
