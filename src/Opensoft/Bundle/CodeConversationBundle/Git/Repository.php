use Opensoft\Bundle\CodeConversationBundle\Git\Diff\DiffHeaderParser;
            $diff = DiffHeaderParser::parse(array_slice($output, $i));
        return DiffHeaderParser::parse($this->repo->git(strtr($command, $parameters)));