<?php
    function openFile($fileName)
    {
        $successOpenFile = fopen($fileName, "r");
        if (!$successOpenFile)
            exit("Error reading file $fileName\n");
        return ($successOpenFile);
    }

    function addToDictionary($letter, &$dictionary)
    {
        if (array_key_exists($letter, $dictionary))
            $dictionary[$letter] += 1;
        else
            $dictionary[$letter] = 1;
    }

    function rememberNames($fileNum)
    {
        $letters;

        $letters["start"] = [];
        while ($name = fgets($fileNum))
        {
            $name = mb_convert_encoding($name, 'Windows-1251', 'UTF-8');
            $i = -1;
            $name = strtolower(trim($name));
            $len = strlen($name);
            addToDictionary($name[0], $letters["start"]);
            while (++$i < $len)
            {
                $current = $name[$i];
                if ($i == $len - 1)
                    $next = "end";
                else
                    $next = $name[$i + 1];
                if (array_key_exists($current, $letters))
                    addTodictionary($next, $letters[$current]);
                else
                    $letters[$current] = [ $next => 1 ];
            }
        }
        fclose($fileNum);
        return ($letters);
    }

    function calculateOccurences(&$chain)
    {
        foreach ($chain as $current => $allNext)
        {
            $count = array_sum($chain[$current]);
            foreach ($allNext as $letter => $frequence)
                $chain[$current][$letter] = $frequence / $count;
        }
    }

    function generateName($chain)
    {
        $name = "";
        $letter = "start";

        while ($letter != "end")
        {
            $key = array_keys($chain[$letter]);
            $key = $key[rand(0, count($chain[$letter]) - 1)];
            $val = $chain[$letter][$key];
            if (rand(0, 100) / 100 <= $val)
            {
                $len = strlen($name);
                if ($key === "end" && $len < 3)
                    continue ;
                if ($key === "end" || $len > 10)
                    return $name;
                $name = $name .
                    mb_convert_encoding($key, 'UTF-8', 'Windows-1251');
                $letter = $key;
            }
        }
    }

    function main($argc, $argv)
    {
        $file = [ "female_names", "male_names" ];

        if ($argc == 1)
        {
            $i = -1;
            while (++$i < 2)
            {
                $fileNum = openFile($file[$i]);
                $chain = rememberNames($fileNum);
                calculateOccurences($chain);
                $serializedChain = serialize($chain);
                file_put_contents("serialized_" . $file[$i], $serializedChain);
            }
        }
        else if ($argc == 3 && is_numeric($argv[2]))
        {
            if ($argv[1] === "f")
                $i = 0;
            else if ($argv[1] === "m")
                $i = 1;
            else
                return ;
            $count = (int)($argv[2]);
            $chain = unserialize(file_get_contents("serialized_" . $file[$i]));
            while ($count-- > 0)
                echo generateName($chain) . "\n";
        }
    }

    main($argc, $argv);
