// @flow
// @jsx h
import type { SettingsProps } from '../types';
import { Collector, h, view } from 'brookjs-silt';
import { i18n } from '../helpers';
import Accounts from './Accounts';
import Header from './Header';
import Highlighting from './Highlighting';
import Messages from './Messages';
import Jobs from './Jobs';
import Runs from './Runs';

export const SettingsPage = ({ stream$ }: ObservableProps<SettingsProps>) => (
    <Collector>
        <div className="wrap">
            <Header stream$={stream$.thru(view(props => ({
                route: props.route.name,
                loading: props.loading
            })))} />

            {stream$.thru(view(props => props.route)).map(route => {
                switch (route.name) {
                    case 'highlighting':
                        return <Highlighting stream$={stream$.thru(view(props => ({
                            ...props // @todo pluck specific props we need
                        })))} />;
                    case 'accounts':
                        return <Accounts stream$={stream$.thru(view(props => ({
                            token: props.token
                        })))} />;
                    case 'jobs':
                        switch (true) {
                            case Boolean(route.parts.run):
                                return <Messages stream$={stream$.thru(view((props: SettingsProps) => ({
                                    job: props.jobs.dict[route.parts.job].name,
                                    job_id: route.parts.job,
                                    status: props.jobs.dict[route.parts.job].runs.dict[route.parts.run].status,
                                    messages: props.jobs.dict[route.parts.job].runs.dict[route.parts.run].messages
                                })))} />;
                            case Boolean(route.parts.job):
                                return <Runs stream$={stream$.thru(view(props => ({
                                    ...props.jobs.dict[route.parts.job]
                                })))} />;
                            default:
                                return <Jobs stream$={stream$.thru(view(props => ({
                                    jobs: props.jobs
                                })))} />;
                        }
                    default:
                        return <div>{i18n('route.404', route)}</div>;
                }
            })}
        </div>
    </Collector>
);
