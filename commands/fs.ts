import fs from 'fs';
import Kefir from 'kefir';

export default {
  access(path: string) {
    return Kefir.fromNodeCallback<void, NodeJS.ErrnoException>(callback =>
      fs.access(path, err => callback(err))
    );
  }
};
