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

	function generateName($chain)
	{
		$name = "";
		$letter = "start";
		while ($letter != "end")
		{
			$count = count($chain[$letter]);
			foreach ($chain[$letter] as $key => $val)
			{
				if (rand(0, $count * 2) < $val)
				{
					if ($key === "end")
					{
						echo $name;
						return ;
					}
					$name = $name .
						mb_convert_encoding($key, 'UTF-8', 'Windows-1251');
					$letter = $key;
					break ;
				}
			}
		}
	}

	function main()
	{
		$fileNum = openFile("female_names");
		$chain = rememberNames($fileNum);
		$name = generateName($chain);
		echo $name . "\n";
	}

	main();
