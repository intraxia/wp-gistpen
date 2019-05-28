import { JobResult, JobSuccess } from '../reducers';

export const jobIsSuccess = (job: JobResult): job is JobSuccess =>
  job.result === 'success';
