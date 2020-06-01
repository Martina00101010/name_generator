<?php

    /*
    **  checking the file can be opened
    */

    function openFile($fileName)
    {
        $successOpenFile = fopen($fileName, "r");
        if (!$successOpenFile) {
            exit("Error reading file $fileName\n");
        }
        return ($successOpenFile);
    }

    /*
    **  adding new letter to chain
    */

    function addToDictionary($letter, &$dictionary)
    {
        if (array_key_exists($letter, $dictionary)) {
            $dictionary[$letter] += 1;
        }
        else {
            $dictionary[$letter] = 1;
        }
    }

    /*
    **  remembering letters from one name
    */

    function rememberOneName($name, &$letters)
    {
        $i = -1;
        $len = strlen($name);

        while (++$i < $len) {
            $currentLetter = $name[$i];
            if ($i == $len - 1) {
                $nextLetter = "end";
            }
            else {
                $nextLetter = $name[$i + 1];
            }
            if (array_key_exists($currentLetter, $letters)) {
                addToDictionary($nextLetter, $letters[$currentLetter]);
            }
            else {
                $letters[$currentLetter] = [ $nextLetter => 1 ];
            }
        }
    }

    /*
    **  iterating through each name and remembering what letters
    **  and how frequently stay after chosen letter
    */

    function rememberNames($fileNum)
    {
        $letters;

        $letters["start"] = [];
        while ($name = fgets($fileNum)) {
            $name = mb_convert_encoding($name, 'Windows-1251', 'UTF-8');
            $name = strtolower(trim($name));
            addToDictionary($name[0], $letters["start"]);
            rememberOneName($name, $letters);
        }
        return ($letters);
    }

    /*
    **  recalculating number of occurences to
    **  frequency in range [0; 1]
    */

    function calculateOccurences(&$chain)
    {
        foreach ($chain as $current => $allNext) {
            $count = array_sum($chain[$current]);
            foreach ($allNext as $letter => $frequence) {
                $chain[$current][$letter] = $frequence / $count;
            }
        }
    }

    /*
    **  creating a sequence of letters
    */

    function generateName($chain)
    {
        $name = "";
        $letter = "start";

        while ($letter != "end") {
            $key = array_keys($chain[$letter]);
            $key = $key[rand(0, count($chain[$letter]) - 1)];
            $val = $chain[$letter][$key];
            if (rand(0, 100) / 100 <= $val) {
                $len = strlen($name);
                if ($key === "end" && $len < 3) {
                    continue ;
                }
                if ($key === "end" || $len > 10) {
                    return $name;
                }
                $name = $name .
                    mb_convert_encoding($key, 'UTF-8', 'Windows-1251');
                $letter = $key;
            }
        }
    }

    /*
    **  checking the serialized model exists and starting name generator
    */

    function generator(&$file, $argv)
    {
        if ($argv[1] === "f") {
            $i = 0;
        } elseif ($argv[1] === "m") {
            $i = 1;
        } else {
            return ;
        }
        $fileContent = @file_get_contents("serialized_" . $file[$i]);
        if ($fileContent === FALSE) {
            echo "error: serialized_" . $file[$i] . " file not found\n";
            exit("type `php main.php` to train model\n");
        }
        $count = (int)($argv[2]);
        $chain = unserialize($fileContent);
        while ($count-- > 0) {
            echo generateName($chain) . "\n";
        }
    }

    /*
    **  opening file, training model, saving model
    */

    function trainChain(&$file)
    {
        $i = -1;
        while (++$i < 2) {
            $fileNum = openFile($file[$i]);
            $chain = rememberNames($fileNum);
            fclose($fileNum);
            calculateOccurences($chain);
            $serializedChain = serialize($chain);
            file_put_contents("serialized_" . $file[$i], $serializedChain);
        }
    }

    function main($argc, $argv)
    {
        $file = [ "female_names", "male_names" ];

        if ($argc == 1) {
            trainChain($file);
        } elseif ($argc == 3 && is_numeric($argv[2])) {
            generator($file, $argv);
        } else {
            echo "usage: php main.php [f/m] [names number]\n";
        }
    }

    main($argc, $argv);
