<?php

use yii\helpers\FileHelper;


if (!function_exists('d')) {

    /**
     * Debug function
     * d($var);
     */
    function d($var, $caller = null)
    {
        if (!isset($caller)) {
            $debug_backtrace = debug_backtrace(1);
            $caller = array_shift($debug_backtrace);
        }
        echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
        echo '<pre>';
        yii\helpers\VarDumper::dump($var, 10, true);
        echo '</pre>';
    }
}

if (!function_exists('dd')) {

    /**
     * Debug function with die() after
     * dd($var);
     */
    function dd($var)
    {
        $debug_backtrace = debug_backtrace(1);
        $caller = array_shift($debug_backtrace);
        d($var, $caller);
        die();
    }
}

if (!function_exists('_lang')) {

    /**
     * @return mixed
     */
    function _lang()
    {
        $lang = explode('-', Yii::$app->language);
        return $lang[0];
    }
}

if (!function_exists('_langFull')) {

    /**
     * @return mixed
     */
    function _langFull()
    {
        return Yii::$app->language;
    }
}

if (!function_exists('_date_current')) {

    /**
     * @param bool $short
     * @return false|string
     */
    function _date_current($short = false)
    {
        $date = date('Y-m-d H:i:s');
        if ($short) {
            $date = date('Y-m-d');
        }
        return $date;
    }
}

if (!function_exists('my_mb_ucfirst')) {

    /**
     * @param $str
     * @return string
     */
    function my_mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }
}

if (!function_exists('is_front')) {

    function is_front()
    {
        return (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index');
    }
}

if (!function_exists('_getFileType')) {

    /**
     * @param $extension
     * @return int|string
     */
    function _getFileType($extension)
    {
        $type = 'other';
        $files = [
            'image' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'svg',
                'bmp',
            ],
            'video' => [
                'avi',
                '3gp',
                'mp4',
                'mov',
                'flv',
                'wmv',
                'mpeg',
            ],
            'office' => [
                'doc',
                'docx',
                'xls',
                'xlsx',
                'ppt',
                'pptx',
                'odt',
                'rtf',
            ],
            'gdocs' => [
                'tif',
                'ai',
                'eps',
            ],
            'pdf' => [
                'pdf',
            ],
            'text' => [
                'txt',
            ],
            'html' => [
                'html',
            ],
            'xml' => [
                'xml',
            ],
            'document' => [
                'zip',
                'gz',
                'odp',
                'ods',
                'bz2',
                'dmg',
                'gz',
                'jar',
                'rar',
                'sit',
            ]
        ];
        foreach ($files as $type => $formats) {
            if (in_array($extension, $formats))
                return $type;
        }
        return $type;
    }

}

if (!function_exists('_uploadFile')) {
    /**
     * @param null $file
     * @param string $type
     * @param string $module
     * @param null $order
     * @param bool $miniOrig
     * @return array
     * @throws \yii\base\Exception
     */
    function _uploadFile($file = null, $type = 'image', $module = 'file', $order = null, $miniOrig = false)
    {
        $_file = [
            'name' => null,
            'path' => null,
            'size' => null,
            'mime' => null,
            'extension' => null,
            'type' => null,
        ];
        $prefix = '/' . $module . '/' . $type;
        $directory = Yii::getAlias('@uploads' . $prefix) . DIRECTORY_SEPARATOR;
        if (!is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }
        $path = null;
        if ($file) {
            $uid = uniqid();
            $order_prefix = !empty($order) ? $order . '_' : '';
            $fileName = $order_prefix . $uid . '.' . $file->extension;
            $filePath = $directory . $fileName;
            $fileSize = $file->size;
            $fileExtension = $file->extension;
            $fileType = $file->type;
            $path = $prefix . '/' . $fileName;
            if ($file->saveAs($filePath)) {
                if ($miniOrig && $type === 'image') {
                    $image = Image::getImagine();
                    $img = $image->open($filePath);
                    $size = $img->getSize();
                    $orig_width = $size->getWidth();
                    $orig_height = $size->getHeight();
                    $delimer = 2;
                    $delimer_min = 3;
                    if ($orig_width > 1000 && $orig_width < 2000) {
                        $delimer = 3;
                        $delimer_min = 4;
                    } elseif ($orig_width >= 2000 && $orig_width < 4000) {
                        $delimer = 4;
                        $delimer_min = 5;
                    } elseif ($orig_width < 1000) {
                        $delimer = 1;
                        $delimer_min = 1.5;
                    }

                    $width = intval($orig_width / $delimer);
                    $height = intval($orig_height / $delimer);
                    $f1 = "m_" . $fileName;
                    $f2 = "o_" . $fileName;
                    $mini_width = intval($orig_width / $delimer_min);
                    $mini_height = intval($orig_height / $delimer_min);

//                \yii\imagine\Image::thumbnail("@webroot{$path}", $orig_width, $orig_height)
//                    ->save($directory.$f2, ['quality' => 90]);

                    Image::thumbnail("@uploads{$path}", $width, $height)
                        ->save($directory . $fileName, ['quality' => 90]);

                    Image::thumbnail("@uploads{$path}", $mini_width, $mini_height)
                        ->save($directory . $f1, ['quality' => 90]);

                }
                $_file['name'] = $fileName;
                $_file['path'] = $path;
                $_file['size'] = $fileSize;
                $_file['mime'] = $fileType;
                $_file['extension'] = $fileExtension;
                $_file['type'] = _getFileType($fileExtension);
            }
        }

        return $_file;

    }
}

if (!function_exists('deleteFile')) {

    /**
     * @param $path
     * @return bool
     */
    function deleteFile($path)
    {
        $deleted = false;

        $directory = Yii::getAlias('@uploads');

        $file_path = $directory . $path;
        if (file_exists($file_path)) {
            $deleted = unlink($file_path);
        }
        if ($deleted) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('is_dir_empty')) {

    /**
     * @param $dir
     * @return bool
     */
    function is_dir_empty($dir)
    {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }

}


if (!function_exists('_cache_file')) {

    function _cache_file($force = false)
    {
        $current = _lang();
//        $sourcePathes = [
//            'backend',
//            'frontend',
//        ];

        $f = Yii::getAlias("@runtime/cache/lang.txt");
        if (file_exists($f)) {
            $langfile_ = file_get_contents($f);
            if ($langfile_ != $current || $force) {
                $fp = fopen($f, 'w+');
                fwrite($fp, $current);
            }
        } else {
            $fp = fopen($f, 'w+');
            fwrite($fp, $current);
        }
    }

}

if (!function_exists('_cache_clear_expired')) {

    /**
     * @param bool $force
     * @throws \yii\base\ErrorException
     */
    function _cache_clear_expired($force = false)
    {
        $sourcePathes = [
            'backend',
            'frontend',
        ];
        $message_all = '';
        $message_tmp = '';
        foreach ($sourcePathes as $sourcePath) {
            $folder = Yii::getAlias("@{$sourcePath}/runtime/cache");
            $dirs = glob($folder . '/*');
            $dateFormat = "D d M Y H:i:s";
            foreach ($dirs as $dir) {
                $dir_is_empty = false;
                if (is_dir($dir)) {
                    $files = FileHelper::findFiles($dir);
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            $file_path = $file;
                            $time = time();
                            if (file_exists($file_path)) {
                                $atime = fileatime($file_path);
                                $mtime = filemtime($file_path);
                                $ctime = filectime($file_path);
                                $removed_time = $time - 2 * 60 * 60;
                                if ($removed_time > $mtime || $force) {
                                    $deleted = unlink($file_path);
                                    $message = Yii::t('helpers', 'Cache cleared');
                                } else {
                                    $message = Yii::t('helpers', 'Cache modified: ') . date($dateFormat, $mtime);
                                }
                            }
                        }
                        $files_ = FileHelper::findFiles($dir);
                        if (empty($files_) && is_dir_empty($dir)) {
                            if (!file_exists($dir)) {
                                $dir_is_empty = true;
                            }
                        }
                    } else {
                        $dir_is_empty = true;
                    }
                    if ($dir_is_empty) {
                        $message = Yii::t('helpers', 'Cache folders cleared');
                        $dirRemoved = FileHelper::removeDirectory($dir);
                    }
                }
            }
            $date = date($dateFormat);
            $message_tmp .= "in <b>{$sourcePath}</b> at: <b>{$date}</b>\n";
        }
        $url = Yii::$app->request->absoluteUrl;
        if (!Yii::$app->user->isGuest) {
            $user_id = Yii::$app->user->identity['id'];
            $user_name = Yii::$app->user->identity['username'];
            $user_info = "<i>id:</i> <b>{$user_id}</b> <i>username:</i> <b>{$user_name}</b>";
        } else {
            $user_info = "<b>Guest</b>\n";
        }
        $message_all .= $message . "\n" . $message_tmp;
        $ip = Yii::$app->request->userIP;
        $method = Yii::$app->request->method;
        $message_all .= "\nUser: " . $user_info . "\n";
        $message_all .= "Method: <code>{$method}</code>" . "\n";
        $message_all .= "Ip: <code>{$ip}</code>" . "\n";
        $message_all .= '<a href="' . $url . '">' . $url . '</a>' . "\n";

//        sendTelegramData('sendMessage', [
//            'chat_id' => 127566656,
//            'text' => $message_all,
//            'parse_mode' => 'HTML'
//        ]);

        return true;
    }

}

if (!function_exists('_send_error')) {

    function _send_error($title, $message, $exception = [], $app = null)
    {
        if (Yii::$app instanceof Yii\console\Application) {
            echo $title;
            var_dump($message);
            TelegramMessageWidget::widget([
                'config' => [
                    'message' => $message
                ]
            ]);
            return 0;
        }
        $blackWords = ['YandexBot', 'TwitterBot', 'apple-touch-icon', 'e-bazar.uz', '/mp/page/service_center', '/category/95ed', '/frontend/web/uploads/', '/themes/mp/assets/fonts/'];
        $userAgent = Yii::$app->request->userAgent;

        $url = Yii::$app->request->absoluteUrl;
        $referrer = Yii::$app->request->referrer;
        $noNeedError = false;
        foreach ($blackWords as $blackWord) {
            $str_tmp = $array = array($userAgent, $url, $referrer);
            $str_tmp = implode(',', $str_tmp);
            $blackWord_ar = explode($blackWord, $str_tmp);
            if (count($blackWord_ar) >= 2) {
                $noNeedError = true;
            }
        }
        if (!$noNeedError) {

            $dateFormat = "D d M Y H:i:s";
            $date = date($dateFormat);

            $query = Yii::$app->request->queryParams;
            $app_id = Yii::$app->controller->module->id;

            $userIP = Yii::$app->request->userIP;
            $userHost = Yii::$app->request->userHost;
            $method = Yii::$app->request->method;

            $message_ = "<b>{$title}</b>: in <b>{$app_id}</b> on <b>{$date}</b>\n";
            $message_ .= '<a href="' . $url . '">' . $url . '</a>' . "\n";
            $message_ .= "<b>{$message}</b>\n";
            $message_ .= '<code>' . json_encode($exception) . '</code>' . "\n";
            $message_ .= '<code>' . json_encode($query) . '</code>' . "\n";

            if (!Yii::$app->user->isGuest) {
                $user_id = Yii::$app->user->identity['id'];
                $user_name = Yii::$app->user->identity['username'];
                $user_info = "<i>id:</i> <b>{$user_id}</b> <i>username:</i> <b>{$user_name}</b>\n";
            } else {
                $user_info = "<b>Guest</b>\n";
            }
            $message_ .= "\nUser: " . $user_info;
            if ($referrer) {
                $message_ .= "Referrer: " . '<a href="' . $referrer . '">' . $referrer . '</a>' . "\n";
            }
            $message_ .= "Method: <code>{$method}</code>" . "\n";
            $message_ .= "userAgent: <code>{$userAgent}</code>" . "\n";
            $message_ .= "userIP: <code>{$userIP}</code>" . "\n";
            if ($userHost) {
                $message_ .= "userHost: <code>{$userHost}</code>" . "\n";
            }

//            sendTelegramData('sendMessage', [
//                'chat_id' => -482854758,
//                'text' => $message_,
//                'parse_mode' => 'HTML'
//            ]);

        }
    }

}

if (!function_exists('encrypt_date')) {

    /**
     * Custom encrypt function
     * return string;
     */
    function encrypt_date()
    {

        $year = intval(date('y'));
        $day = date('z');

        $prefix = ($year * 1000);
        $prefix += $day;

        return $prefix;
    }

}

if (!function_exists('encrypt_unique_number')) {

    /**
     * @param int $a
     * @param null $b
     * @return mixed|null|string|string[]
     */
    function encrypt_unique_number($a = 0, $b = null)
    {

        if (empty($b)) {
            $b = encrypt_date();
        }
        $_1 = ($a + $b);
        $_2 = ($a + $b + 1);
        $_3 = ($_1 * $_2);
        $_4 = ($_3 / 2);
        $result = $_4 + $b;
        $base_convert_32 = base_convert($result, 10, 32);
        $base_convert_32 = mb_strtoupper($base_convert_32);
        return $base_convert_32;
    }

}

if (!function_exists('encrypt_unique_number')) {

    /**
     * @param $number
     * @return array
     */
    function decrypt_unique_number($number)
    {

        $result = [
            'a' => null,
            'b' => null,
        ];

        $base_convert_32 = mb_strtolower($number);
        $number = base_convert($base_convert_32, 32, 10);

        $_1 = (8 * $number);
        $_2 = sqrt($_1 + 1);
        $_3 = ($_2 - 1);
        $_4 = floor($_3 / 2);

        $_5 = ($_4 + 1);
        $_6 = ($_4 * $_5);
        $_7 = ($_6 / 2);
        $_8 = ($number - $_7);

        $_9 = ($_4 - $_8);
        $result['a'] = $_9;
        $result['b'] = $_8;

        return $result;
    }
}

if (!function_exists('clear_phone')) {
    /**
     * @param $phone
     * @return bool|string
     */
    function clear_phone($phone)
    {
        $number = preg_replace('/\D/', '', $phone);
        if (strlen($number) < 9)
            return false;
        return substr($number, -9);
    }
}

if (!function_exists('get_phone_without_code')) {
    /**
     * @param $phone
     * @return bool|string
     */
    function get_phone_without_code($phone)
    {
        $number = preg_replace('/\D/', '', $phone);
        if (strlen($number) > 9)
            return substr($number, -9);
        else
            return $number;
    }
}


if (!function_exists('clear_phone_full')) {
    /**
     * @param $phone
     * @return bool|string
     */
    function clear_phone_full($phone)
    {
        $number = preg_replace('/\D/', '', $phone);
        if ($number && ctype_digit($number) && strlen($number) === 9) {
            $number = '998' . substr($number, -9);
        }
        return $number;
    }
}


if (!function_exists('add_phone_mask')) {
    /**
     * @param $phone
     * @return bool|string
     */
    function add_phone_mask($phone)
    {
        $data = clear_phone_full($phone);
        if (preg_match('/(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})/', $data, $matches)) {
            $result = '+' . $matches[1] . $matches[2] . '-' . $matches[3]. '-' . $matches[4] . $matches[5];
            return $result;
        }


        //$data = '+' . clear_phone_full($phone);
        /*
                $data = '+112 34 567 890';

                if( preg_match( '/^\+\d(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})$/', $data,  $matches ) )
                {
                    $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
                    return $result;
                }*/
    }
}


if (!function_exists('set_history')) {

    /**
     * @param $request
     * @param $response
     */
    function set_history($request, $response, $method = 'api', $params = null)
    {
        try {
            $user = Yii::$app->user->identity;
            $model = new RequestHistory();
            $model->method = $method;
            $model->params = $params;
            $model->user_id = $user['id'] ?: null;
            $model->request = json_encode($request);
            $model->response = json_encode($response);
            $model->save();
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}

if (!function_exists('clear_card')) {
    /**
     * @param $phone
     * @return bool|string
     */
    function clear_card($card_number)
    {
        $number = preg_replace('/\D/', '', $card_number);
        if (strlen($number) < 16)
            return false;
        return substr($number, -16);
    }
}

if (!function_exists('clear_card_expire')) {
    /**
     * @param $card_expire
     * @return bool|string
     */
    function clear_card_expire($card_expire)
    {
        $card_expire = preg_replace('/\D/', '', $card_expire);
        if (strlen($card_expire) < 4)
            return false;
        return substr($card_expire, -4);
    }
}

// fallback for getallheaders(), e.g. for nginx
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

if (!function_exists('_generate_error')) {

    function _generate_error($errors = [])
    {
        $flash_errors = null;
        $index = 0;
        if (is_array($errors)) {
            foreach ($errors as $model_error) {
                if (is_array($model_error)) {
                    foreach ($model_error as $error) {
                        $flash_errors[$index++] = $error;
                    }
                } else {
                    $flash_errors[$index++] = $model_error;
                }
            }
        } else {
            $flash_errors = [$errors];
        }
        return $flash_errors;
    }
}


if (!function_exists('str_split_unicode')) {

    function str_split_unicode($str, $l = 0)
    {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}

if (!function_exists('translit')) {

    /**
     * Translit UZ-RU RU-UZ
     *
     * @param string $text
     * @param string $lang
     * @return string
     */
    function translit($text, $lang = 'uz')
    {

        $rus = array('Ю', 'Я', 'Ч', 'Ш', 'Щ', 'А', 'Б', 'В', 'Г', 'Д', ' Е', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', ' Ц', 'Ц', 'Ъ', 'Ы', 'Ь', 'Э', 'ч', 'ш', 'щ', 'ю', 'я', 'а', 'б', 'в', 'г', 'д', ' е', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', ' ц', 'ц', 'ъ', 'ы', 'ь', 'э', 'Ҳ', 'ҳ', 'Қ', 'қ', 'Ғ', 'ғ', 'Ў', 'ў');

        $lat = array('Yu', 'Ya', 'Ch', 'Sh', 'Sh', 'A', 'B', 'V', 'G', 'D', ' Ye', 'E', 'Yo', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'X', ' S', 'Ts', '’', 'I', '', 'E', 'ch', 'sh', 'sch', 'yu', 'ya', 'a', 'b', 'v', 'g', 'd', ' ye', 'e', 'yo', 'j', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', ' s', 'ts', '’', 'i', '', 'e', 'H', 'h', 'Q', 'q', 'G’', 'g’', 'O’', 'o’');

        if ($lang == 'uz') {
            return str_replace($rus, $lat, $text);
        } else if ($lang == 'ru') {
            return str_replace($lat, $rus, $text);
        }

        return $text;
    }
}

if (!function_exists('renderSearchQueryCondition')) {

    /**
     * @param $str
     * @return array
     */
    function renderSearchQueryCondition($str)
    {
        $str_0 = translit($str, 'ru');

        $full_str = $str . ' ' . $str_0;

        $result = [];
        $query_array = str_split_unicode($full_str, 3);
        foreach ($query_array as $index => $item) {
            $len = mb_strlen($item, "UTF-8");
            if ($len > 2) {
                $result[] = "$item";
            }
        }

        return $result;
    }
}


if (!function_exists('prizmaUz')) {

    function prizmaUz($isSll = true)
    {
        if ($isSll)
            return 'https://prizma.uz';
        else
            return 'http://prizma.uz';

    }
}


if (!function_exists('session')) {

    function session()
    {
        $session = Yii::$app->session;
        if (!$session->isActive) $session->open();
        return $session;
    }
}

if (!function_exists('setSession')) {

    function setSession($key, $value)
    {
        session()->set($key, $value);
    }
}

if (!function_exists('getSession')) {

    function getSession($key, $defaultValue = null)
    {
        return session()->get($key, $defaultValue);
    }
}

if (!function_exists('removeSession')) {

    function removeSession($key, $all = false)
    {
        if ($all)
            session()->removeAll();
        else {
            if (session()->has($key))
                session()->remove($key);
        }
    }
}

if (!function_exists('createFolder')) {

    function createFolder($path)
    {
        if (!file_exists($path)) {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true);
        }

        if (file_exists($path)) {
            return $path;
        }
    }
}

if (!function_exists('getClassMethodName')) {        // get class and method name without namespace

    function getClassMethodName($classMethod)
    {
        $resArr = explode('\\', $classMethod);
        $classMethodName = array_pop($resArr);

        $res = explode('::', $classMethodName);
        $className = $res[0];
        $methodName = $res[1];
        return [
            'className' => $className,
            'methodName' => $methodName,
        ];
    }
}


if (!function_exists('headerLocation')) {        // get class and method name without namespace

    function headerLocation($link)
    {
        header("Location: $link");
    }


}
if (!function_exists('priceSumName')) {
    /**
     * @return bool|string
     */
    function priceSumName($price)
    {
        return number_format((float)$price, 2, '.', ' ');
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}

function human_filesize($bytes, $decimals = 2) {
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}