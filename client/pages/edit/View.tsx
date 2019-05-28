import React from 'react';
import { Commits, EditPage } from '../../components';

const View: React.FC<{
  route: string | null;
  edit: React.ComponentProps<typeof EditPage>;
  commits: React.ComponentProps<typeof Commits>;
}> = ({ route, edit, commits }) => {
  switch (route) {
    case 'editor':
      return <EditPage {...edit} />;
    case 'commits':
      return <Commits {...commits} />;
    default:
      return null;
  }
};

export default View;
