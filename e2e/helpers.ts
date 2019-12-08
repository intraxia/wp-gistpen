import path from 'path';
import execa from 'execa';

export const resetSite = () => execa(path.join(__dirname, 'reset-site.sh'));
