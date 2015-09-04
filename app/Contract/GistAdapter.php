<?php
namespace Intraxia\Gistpen\Contract;

interface GistAdapter
{
    /**
     * Convert the model's data into a Gist array.
     *
     * Pulls the data out of the object and formats it into the
     * array formatting required to send to the GitHub Gist API.
     *
     * @return array
     */
    public function toGist();

    /**
     * Retrieves the Gist sha for the model.
     *
     * The Gist sha is the Gist ID for the overall Gist.
     * This is to differentiate it from the Gist's version sha
     * pointing to a specific Gist commit.
     *
     * @return string
     */
    public function getGistSha();

    /**
     * Sets the Gist sha on the model.
     *
     * @param string $sha
     */
    public function setGistSha($sha);
}
