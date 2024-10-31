#!/usr/bin/env bash
SVN_FILE=/Users/gaupoit/Work/yme/svn/protect-pages-posts/

rsync -arv --exclude=.* --exclude=deployment/assets/* ./ $SVN_FILE"/trunk"
rsync -arv deployment/assets/ $SVN_FILE"/assets"

cd $SVN_FILE
svn add --force .
svn commit -m "Bumping new version"

