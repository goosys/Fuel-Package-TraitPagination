<?php $keyword = ( isset($params) && isset($params['keyword']) && count($params['keyword']))? '「'.implode(' ',$params['keyword']).'」の検索結果': '';?>
<?php View::set_global('keyword', $keyword, false); ?>