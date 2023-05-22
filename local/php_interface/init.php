<?
use Bitrix\Main\IO;
use Bitrix\Main\Application;

    function p ($elem) {
        echo '<pre>';
        print_r($elem);
        echo '</pre>';
    }

/**
     * Записывает переданный объект в файл #USER_ID#-dump.html, обработав функцией print_r
    *
    * @param mixed $obj Объект, который необходимо записать в файл
    *
    * @return void
    *
    * @example
    * <pre>
    * p2f($arFields, 1)
    * </pre>
    *
    */
    function p2f($obj, $fileName = false)
    {
        global $USER;
        $id = $fileName ? $fileName : ($USER->GetID() ? $USER->GetID() : 'guest');
 
        $dump = "<pre style='font-size: 11px; font-family: tahoma;'>" . print_r($obj, true) . "</pre>";
 
        //создаем файл
        $file = new IO\File(Application::getDocumentRoot().'/upload/p2f/'. $id . "-dump.html");
        $file->putContents($dump, IO\File::APPEND);
    }
 ?>