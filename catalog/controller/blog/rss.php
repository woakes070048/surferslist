<?php
class ControllerBlogRSS extends Controller {
    public function index() {
        $this->load->model('setting/setting');

        $setting_info = $this->model_setting_setting->getSetting('blog', $this->config->get('config_store_id'));

        if (!$setting_info) {
            return false;
        }

        $this->load->model('blog/article');
        $this->load->model('tool/image');

        $output = '<?xml version="1.0" encoding="UTF-8" ?>';
        $output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $output .= '<channel>';
        $output .= '<title>' . $setting_info['blog_title'][$this->config->get('config_language_id')] . '</title>';
        $output .= '<description>' . $setting_info['blog_meta_description'][$this->config->get('config_language_id')] . '</description>';
        $output .= '<link>' . HTTP_SERVER . '</link>';

        $articles = $this->model_blog_article->getBlogArticles();

        foreach ($articles as $article) {
            if ($article['description']) {
                $output .= '<item>';
                $output .= '<title>' . $article['name'] . '</title>';
                $output .= '<link>' . $this->url->link('blog/article', 'blog_article_id=' . $article['blog_article_id']) . '</link>';
                $output .= '<description>' . $article['description'] . '</description>';
                $output .= '<g:condition>new</g:condition>';
                $output .= '<g:id>' . $article['blog_article_id'] . '</g:id>';

                if ($article['image'] && is_file(DIR_IMAGE . $article['image'])) {
                    $output .= '<g:image_link>' . $this->model_tool_image->resize($article['image'], $this->config->get('blog_image_thumb_width'), $this->config->get('blog_image_thumb_height')) . '</g:image_link>';
                }

                $output .= '</item>';
            }
        }

        $output .= '</channel>';
        $output .= '</rss>';

        $this->response->addHeader('Content-Type: application/rss+xml');
        $this->response->setOutput($output);
    }
}
