// @flow

export type MessageLevel = 'success';

export type Message = {
    ID : string;
    run_id : string;
    text : string;
    level : MessageLevel;
    logged_at : string;
};

export type RunStatus = 'scheduled' | 'finished';

export type Run = {
    ID : string;
    job : string;
    status : RunStatus;
    scheduled_at : string;
    started_at : string;
    finished_at : string;
    rest_url : string;
    job_url : string;
    console_url : string;
    messages? : Array<Message>;
};

export type JobStatus = 'idle' | 'processing';

export type Job = {
    name : string;
    slug : string;
    description : string;
    rest_url : string;
    runs_url : string;
    status? : JobStatus;
    runs? : Array<Run>;
};
