<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 4/12/20
 * Time: 11:40 AM
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class NewsController extends Controller
{
    public function getNews(Request $request)
    {
        $news_id=$request['news_id'];
        $sql = "select cpa_security.cpa_general.get_news_one(:id) from dual";
        $news = DB::selectOne($sql,['id' => $news_id]);

        $newsView= view('news.index', ['data' => $news])->render();

        return response()->json(array(
            'success' => true,
            'newsView'=>$newsView,

        ));

    }

    public function downloadAttachment($news_id) {



        $sql = "select cpa_security.cpa_general.get_news_one(:id) from dual";
        $news = DB::selectOne($sql,['id' => $news_id]);

        if($news) {
            if($news->attachment_filename && $news->attachment_content) {
                $fileArr = explode('.', $news->attachment_filename);
                $content = $news->attachment_content;
                // echo $content; die();
                $contentType = $this->getContentType($fileArr[count($fileArr)-1]);
                $filename = $news->attachment_filename;

                if (preg_match('/;base64,/', $content)) {
                    $content = substr($content, strpos($content, ',') + 1);
                    $content = base64_decode($content);
                }

                return response()->make($content, 200, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"'
                ]);
            }
            die("No Attachment found!!");
        }
    }

    private $fileTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'png' => 'image/png',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
    ];

    private function getContentType($fileType)
    {
        try{
            $contentType = $this->fileTypes[$fileType];
            if($contentType) {
                return $contentType;
            }
        }catch(\Exception $e){
            return '';
        }

    }


}
