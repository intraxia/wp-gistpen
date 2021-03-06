import React from 'react';
import { View } from '../View';
import { defaultGlobals } from '../../../../globals';
import { JsonError } from '../../../../api';

export default {
  title: 'Creating View',
};

export const chooseOrNew = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View status="choose-or-new-repo" globals={defaultGlobals} />
  </div>
);

export const chooseExisting = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="choose-existing"
      globals={defaultGlobals}
      filename="filename.js"
      saving={false}
      error={null}
    />
  </div>
);

export const chooseExistingSaving = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="choose-existing"
      globals={defaultGlobals}
      filename="filename.js"
      saving={true}
      error={null}
    />
  </div>
);

export const chooseExistingError = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="choose-existing"
      globals={defaultGlobals}
      filename="filename.js"
      saving={true}
      error={new JsonError(new TypeError('An error occurred!'))}
    />
  </div>
);

export const createNew = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="create-new"
      description="A description"
      filename="filename.js"
      globals={defaultGlobals}
      saving={false}
      error={null}
    />
  </div>
);

export const createNewSaving = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="create-new"
      description="A description"
      filename="filename.js"
      globals={defaultGlobals}
      saving={true}
      error={null}
    />
  </div>
);

export const createNewError = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      status="create-new"
      description="A description"
      filename="filename.js"
      globals={defaultGlobals}
      saving={false}
      error={new JsonError(new TypeError('An error occurred!'))}
    />
  </div>
);
