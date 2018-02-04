// @flow

export type MessageLevel = 'error' | 'warning' | 'success' | 'info' | 'debug';

export type Message = {
    ID: string;
    run_id: string;
    text: string;
    level: MessageLevel;
    logged_at: string
};

export type RunStatus = 'scheduled' | 'running' | 'paused' | 'finished' | 'error';

export type Run = {
    ID: string;
    job: string;
    status: RunStatus;
    scheduled_at: string;
    started_at: string | null;
    finished_at: string | null;
    rest_url: string;
    job_url: string;
    console_url: string;
    messages?: Array<Message>
};

export type JobStatus = 'idle' | 'processing';

export type Job = {
    name: string;
    slug: string;
    description: string;
    rest_url: string;
    runs_url: string;
    status?: JobStatus;
    runs?: Array<Run>
};
