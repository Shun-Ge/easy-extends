<?php

namespace DavidNineRoc\EasyExtends\Filesystem;


use DavidNineRoc\EasyExtends\Support\Str;

class IniReader extends IniOperate
{
    /**
     * @param Filesystem $filesystem
     * @return array
     */
    public function readIni(Filesystem $filesystem)
    {
        $iniContent = $filesystem->get($this->iniFile);


        return $this->readString($iniContent);
    }

    /**
     * 从字符串中解析 ini 内容成为数组
     *
     * @param $iniContent
     * @return array
     */
    protected function readString($iniContent)
    {
        // 最后加一个换行，切割方便
        $iniContent .= "\n";

        $array = $this->readStringToArray($iniContent);

        return $array;
    }

    /**
     * 从字符串中解析 ini 内容成为数组
     *
     * @param $iniContent
     * @return array
     */
    protected function readStringToArray($iniContent)
    {
        $ini = $this->splitIniContentIntoLines($iniContent);


        $section = '';
        foreach ($ini as $line) {

            $line = (new Str($line))->trimSpace()->replaceSpace()->toString();

            if ($this->isComment($line)) {
                continue;
            } elseif ($this->isSection($line)) {
                $section = $this->getSectionName($line);
                $this->setSection($section);
                continue;
            }
            /************************************
             * 每个 section 下面会有多个 key=value
             * 把他们存到 section 模块下面
             */
            list($key, $value) = explode('=', $line, 2);

            $key = (new Str($key))->trimSpace()->replaceSpace()->toString();
            $value = (new Str($value))->trimSpace()->replaceSpace()->toString();
            $this->setValue($section, $key, $value);
        }


        var_dump($this->iniValues['PHP']['extension']);
    }

    /**
     * 把 ini 内容通过换行符分隔变成简单的数组
     *
     * @param $iniContent
     * @return array
     */
    protected function splitIniContentIntoLines($iniContent)
    {
        if (is_string($iniContent)) {
            $iniContent = explode("\n", str_replace("\r", "\n", $iniContent));
        }

        return $iniContent;
    }
}