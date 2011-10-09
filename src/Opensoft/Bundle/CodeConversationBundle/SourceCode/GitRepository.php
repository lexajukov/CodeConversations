use Opensoft\Bundle\CodeConversationBundle\Model\FileDiff;
use Opensoft\Bundle\CodeConversationBundle\Model\FileDiffChunk;
        $format = '%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%P|%s';
//        $format = '%H%n%s%n%cn%n%ai%n%P%n';
        foreach ($output as $line) {
            $infos = explode('|', $line);
            $commit = new Commit();
            $commit->setId($infos[0]);
            $commit->setTree($infos[1]);
            $commit->setAuthorName($infos[2]);
            $commit->setAuthorEmail($infos[3]);
            $commit->setAuthoredDate(new \DateTime($infos[4]));
            $commit->setCommitterName($infos[5]);
            $commit->setCommitterEmail($infos[6]);
            $commit->setCommittedDate(new \DateTime($infos[7]));
            $commit->setParents(explode(' ',$infos[8]));
            $commit->setMessage($infos[9]);

            $commits[] = $commit;
        }

//        $commits = array();
//        $output = explode("\n", trim($process->getOutput()));
//        $i = 0;
//        do {
//            if (!empty($output[$i])) {
//                $commit = new Commit();
//                $commit->setSha1($output[$i]);
//                $commit->setMessage($output[$i+1]);
//                $commit->setAuthor($output[$i+2]);
//                $commit->setTimestamp(new \DateTime($output[$i+3]));
//
//                // Detect merge parent
//                if (strpos($output[$i+4], " ") > 0) {
//                    $merge = explode(" ", $output[$i+4]);
//                    $commit->setParents($merge);
//                } else {
//                    $commit->addParent($output[$i+4]);
//                }
//
//                $commits[] = $commit;
//                $i += 6;
//            } else {
//                $i++;
//            }
//        } while ($i <= count($output));
        $format = '%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%P|%s';

//        $format = '%H%n%s%n%cn%n%ai%n%P%n';
        foreach ($output as $line) {
            $infos = explode('|', $line);
            $commit = new Commit();
            $commit->setId($infos[0]);
            $commit->setTree($infos[1]);
            $commit->setAuthorName($infos[2]);
            $commit->setAuthorEmail($infos[3]);
            $commit->setAuthoredDate(new \DateTime($infos[4]));
            $commit->setCommitterName($infos[5]);
            $commit->setCommitterEmail($infos[6]);
            $commit->setCommittedDate(new \DateTime($infos[7]));
            $commit->setParents(explode(' ',$infos[8]));
            $commit->setMessage($infos[9]);

            $commits[] = $commit;
        }

//
//        $i = 0;
//        do {
//            if (!empty($output[$i])) {
//                $commit = new Commit();
//                $commit->setSha1($output[$i]);
//                $commit->setMessage($output[$i+1]);
//                $commit->setAuthor($output[$i+2]);
//                $commit->setTimestamp(new \DateTime($output[$i+3]));
//
//                // Detect merge parent
//                if (strpos($output[$i+4], " ") > 0) {
//                    $merge = explode(" ", $output[$i+4]);
//                    $commit->setParents($merge);
//                } else {
//                    $commit->addParent($output[$i+4]);
//                }
//
//                $commits[] = $commit;
//                $i += 6;
//            } else {
//                $i++;
//            }
//        } while ($i <= count($output));
        $format = '%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%P|%s%n%b';
//        $format = '%H%n%s%n%cn%n%ai%n%P%n%b';
        $commits = array();
        $output = explode("\n", trim($process->getOutput()));
//        foreach ($output as $line) {

//        print_r($output);
//        die();

        $infos = explode('|', $output[0]);
        $commit->setId($infos[0]);
        $commit->setTree($infos[1]);
        $commit->setAuthorName($infos[2]);
        $commit->setAuthorEmail($infos[3]);
        $commit->setAuthoredDate(new \DateTime($infos[4]));
        $commit->setCommitterName($infos[5]);
        $commit->setCommitterEmail($infos[6]);
        $commit->setCommittedDate(new \DateTime($infos[7]));
        $commit->setParents(explode(' ',$infos[8]));
//        $commit->setMessage($infos[9]);
        $message = $infos[9];

//            $commits[] = $commit;
//        }



//
//        $commit = new Commit();
//        $commit->setSha1($output[0]);
//        $message = $output[1];
////        $commit->setMessage($output[1]);
//        $commit->setAuthor($output[2]);
//        $commit->setTimestamp(new \DateTime($output[3]));
//
//        // Detect merge parent
//        if (strpos($output[4], " ") > 0) {
//            $merge = explode(" ", $output[4]);
//            $commit->setParents($merge);
//        } else {
//            $commit->addParent($output[4]);
//        }
//
        $i = 1;
            $diff = $this->parseDiffOutput(array_slice($output, $i));
            $commit->setDiff($diff);
        $diffFile = null;
        $fileDiffs = array();
                if (null !== $diffFile) {
                        $diffFile->addFileDiffChunk($diffChunk);
                    $fileDiffs[] = $diffFile;
                $diffFile = new FileDiff();
                $diffFile->setSrcPath(substr($srcFileName, 2));
                $diffFile->setDstPath(substr($dstFileName,2));
                        $diffFile->setSrcMode(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                        $diffFile->setDstMode(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                        $diffFile->setDstMode(substr($line, 18));
                        $diffFile->setStatus(FileDiff::STATUS_DELETION);
                        $diffFile->setDstMode(substr($line, 14));
                        $diffFile->setStatus(FileDiff::STATUS_ADDITION);
                        $diffFile->setSrcPath(substr($line, 10));
                        $diffFile->setStatus(FileDiff::STATUS_COPY);
                        $diffFile->setDstPath(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_COPY);
                        $diffFile->setSrcPath(substr($line, 12));
                        $diffFile->setStatus(FileDiff::STATUS_RENAMING);
                        $diffFile->setDstPath(substr($line, 10));
                        $diffFile->setStatus(FileDiff::STATUS_RENAMING);
                            $diffFile->setSrcMode($mode);
                            $diffFile->setDstMode($mode);
                            $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                        $diffFile->setSrcSha1($srcHash);
                        $diffFile->setDstSha1($dstHash);
                            $diffFile->setSrcPath(substr($line, 6));
                            $diffFile->setDstPath(substr($line, 6));
                    $diffFile->addFileDiffChunk($diffChunk);
                $diffChunk = new FileDiffChunk();
        if (null !== $diffFile) {
                $diffFile->addFileDiffChunk($diffChunk);
            $fileDiffs[] = $diffFile;
        $diff = new Diff();
        $diff->setFileDiffs($fileDiffs);

        return $diff;
                throw new BuildException(sprintf('Unable to get HEAD for revision "%s".', $revision));