<?php

//ビンゴアプリのインスタンスを作って実行する
$bingoApp = new BingoApp();
$bingoApp->execute();

class BingoApp
{
    protected int $size;
    public function __construct()
    {
        //ビンゴのサイズ
        $this->size = $this->stdinInt();
    }
    public function execute()
    {
        $bingo_words = $this->getBingoWords();
        $word_count = $this->stdinInt();
        $chosen_words = $this->getChosenWords($word_count);
        $search_resuluts = $this->getSearchResults($bingo_words, $chosen_words);
		//ビンゴの結果を出力する
		$bingo_result = $this->getBingoResult($search_resuluts);

        if ($bingo_result) {
            echo 'yes';
        } else {
            echo 'no';
        }
    }
    public function stdinInt(): int
    {
        return (int)trim(fgets(STDIN));
    }
    /**
     * ビンゴの単語を多次元配列にして返す
     * 例）3×3の場合
     * [
     * 	0 => [a, b, c]
     * 	1 => [d, e, f]
     * 	2 => [g, h, i]
     * ]
     * @return array
     */
    public function getBingoWords(): array
    {
        $num = 0;
        $bingo_words = [];
        while ($num < $this->size) {
            $bingo_words[$num] = explode(" ", trim(fgets(STDIN)));
            $num++;
        }
        return $bingo_words;
    }
    /**
     * 選ばれた単語の配列を作る
	 * 例)「a」「b」「c」の場合
     * [ 0 => a, 1=> b, 2 => c ]
     *
     * @param int $word_count
     * @return array
     */
    public function getChosenWords(int $word_count): array
    {
        $num = 0;
        $chosen_words = [];
        while ($num < $word_count) {
            $chosen_words[$num] = trim(fgets(STDIN));
            $num++;
        }
        return $chosen_words;
    }
	/**
	 * 「選ばれた単語」と「ビンゴの単語」の照合結果を返す
	 *
	 * @param array $bingo_words
	 * @param array $chosen_words
	 * @return array
	 */
	public function getSearchResults(array $bingo_words, array $chosen_words): array
	{
		foreach ($chosen_words as $chosen_word) {
			$search_resuluts[] = $this->collationWord($bingo_words, $chosen_word);
		}
		//配列からnullを削除
		$search_resuluts = array_filter($search_resuluts, function ($search_resulut) {
			return !is_null($search_resulut);
		});
        return $search_resuluts;
	}
	/**
	 * 「選ばれた単語」を「ビンゴの単語」と照合し
	 * 見つかった場合「2つ目の添字」を、見つからなかった場合に「null」を返す
	 * 例)
	 * a b c
	 * d e f
	 * g h i
	 * 上記のビンゴを多次元配列とした場合、「b」の位置は[0][1]なので1が返される
	 *
	 * @param array $bingo_words
	 * @param string $chosen_word
	 * @return integer|null
	 */
    public function collationWord(array $bingo_words, string $chosen_word): ?int
    {
        foreach ($bingo_words as $words) {
            if (($search = array_search($chosen_word, $words)) !== false) {
                $search_resulut = $search;
                break;
            } else {
                $search_resulut = null;
            }
        }
        return $search_resulut;
    }

    /**
     * ビンゴの結果を返す
     *
     * @param integer $size
     * @param array $search_resuluts
     * @return void
     */
    public function getBingoResult(array $search_resuluts)
    {
        rsort($search_resuluts);
        /**
         * 縦一列
         * 3×3の場合照合結果で[0][1][2]いずれかが3つあると成立
         */
        if (array_search($this->size, array_count_values($search_resuluts)) !== false) {
            return true;
        }
        /**
         * 横一列or斜め一列
         * 3×3の場合照合結果の重複する値を削除して[0][1][2]を満たすと成立
         */
        $bingo_result = false;
        foreach (array_values(array_unique($search_resuluts)) as $search_result) {
            $this->size--;
            if ($search_result == $this->size) {
                $bingo_result = true;
            } else {
                $bingo_result = false;
            }
        };
        return $bingo_result;
    }
}
