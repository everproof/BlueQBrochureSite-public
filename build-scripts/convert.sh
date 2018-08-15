grep -rl '<VERSION>' ./ | xargs sed -i 's/<VERSION>/1234/g'
