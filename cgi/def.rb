#!/usr/local/bin/ruby
require 'cgi'
require 'mysql2'
require 'open-uri'
require 'json'

#検索語の受け取り整形
def makewords(cgi)
  word="q_date:#{cgi["q_date"]} q_content:#{cgi["q_content"]} q_belonging:#{cgi["q_date"]}\s
a_date:#{cgi["a_date"]} a_content:#{cgi["a_content"]} a_belonging:#{cgi["a_belonging"]}\s
c_tag:#{cgi["c_tag"]} #{cgi["search"]}"
  words=word.gsub(/[\r\n]/,"").gsub(/q_(.*?):/,"question_\1:").
	gsub(/a_(.*?):/,"answer_\1:").sub(/c_(.*?):/,"category_\1:").
	gsub(/<|>|""/,"").split(/[\+\s 　]+/).
        delete_if{|i|i=~/[aA][nN][dD]/||i=~/^[A-Z]+:$/||i==""}
  (words.size-1).times{|i|words.insert(2*i+1,"or")} if cgi["andor"]=="or"
  return words
end
#検索語の分別
def makekeys(words)
  keys=[]
  words.each{|i|
    if i=="OR"||i=="or"
      keys.push("OR")
    elsif i=~/([a-z]+?):/
      keys.push(Hash[*i.split(":")])
    else
      keys.push(i)
    end
  }
  return keys
end
#全てのフィールドから探す奴の文生成する関数:返り値は
def all_any_search(key,db)
  sql= <<SQL
SELECT DISTINCT question.id
FROM            question
JOIN answer
ON question.id = answer.id
JOIN category
ON answer.id = category.id
WHERE question.id = ANY         (
                                       SELECT id
                                       FROM   question
                                       WHERE  date LIKE '%#{key}%'
                                       OR     content LIKE '%#{key}%'
                                       OR     belonging LIKE '%#{key}%')
OR question.id  = ANY
                                (
                                       SELECT id
                                       FROM answer
                                       WHERE date LIKE '%#{key}%'
                                       OR content LIKE '%#{key}%'
                                       OR belonging like '%#{key}%')
OR question.id = ANY
                                (
                                       SELECT id
                                       FROM category
                                       WHERE tag LIKE '%#{key}%')
SQL
  data=[]
  db.query(sql).each{|row|
    data<<row[0]
  }
  return data
end
#指定したフィールドから個別検索
def field_search_s(key,db)
attr=key.keys[0].split("_")
  sql = <<SQL
SELECT DISTINCT question.id 
FROM            question
JOIN answer
ON question.id = answer.id
JOIN category
ON answer.id = category.id
WHERE question.id = ANY         (
                                       SELECT id
                                       FROM   #{attr[0]}
                                       WHERE  #{attr[1]} LIKE '%#{key.values[0]}%')
SQL
  data=[]
  begin
  db.query(sql).each{|row|
    data<<row[0]
  }
  rescue
    data<<[]
  end
  return data
end
#検索実行
def andor(keys,db)
  result_each=[]
  keys.each{|i|
    result_each<<
    if i=="OR"
      i
    elsif i.kind_of?(Hash)
      field_search_s(i,db)
    else
      all_any_search(i,db)
    end
  }
  return result_each
end
#検索式解釈
def strinterpret(keys)
  hit,f="",0
  keys.each{|i|
    if hit.empty? && i!="OR"
      hit=i
    elsif i=="OR"
      f=1
    elsif f==1
      hit=i|hit
      f=0
    else
      hit=i&hit
    end
  }
  return hit
end
#ヒットしたデータ(NBCのみ)を元に問い合わせ
def retr_hitdata(hit,db)
  data=[]
  hit.each{|a|
    sql= <<SQL
SELECT *
FROM            question
JOIN answer
ON question.id = answer.id
JOIN category
ON answer.id = category.id
WHERE question.id = #{a}
SQL
    db.query(sql).each{|row|
      data<<row
    }
  }
  return data
end
#ページリンクの生成
def create_paging_link(hits,par)
  par["ps"]=["20"] if par["ps"]==[""]
  # par["ps"]||=["20"]
  # par["ps"]=["20"] if par["ps"][0].to_i<0
  p_now,p_size,hits=par["p"][0].to_i,par["ps"][0].to_i,hits.to_i
  begin
  hmp=hits%p_size!=0&&hits!=0? (hits/p_size)+1 : hits/p_size
  rescue ZeroDivisionError
    hmp=0
  end
  pagelinks="<table><tr>\n"
  hmp.times{|i|
      link="/cgi/data_table.cgi?"
      par["p"]=[i]
      par.each{|k,v|link+="#{k}=#{v[0]}&"}
      pagelinks+="<td><a href=\"#{link.chop}\"><b>#{i+1}</b></a></td>\n"
      pagelinks+="</tr>\n<tr>" if i%30==0&&i!=0
  }
  par["p"]=[p_now.to_s]
  pagelinks+="</tr></table>\n"
  return pagelinks
end
#表作る
def create_table_html(data,par)
  tab=""
  data[par["p"][0].to_i*par["ps"][0].to_i,par["ps"][0].to_i].each{|row|
    tab+= <<-EOS
    <tr>
    <td class="result"><a href="accurate.cgi?id=#{row[0]}">#{row[0]}</a></td>
    <td class="result">#{row[2][0,30]}</td>
    <td class="result">#{row[5][0,30]}</td>
    <td class="result">#{row[7]}</td>
    </tr>
EOS
  }
  return tab
end
#GET値を引継/保存しておく
def rep_hide(per)
  h=""
  v=per.delete("ps")
  per.each{|k,v|
    h+="<input type=\"hidden\" name=\"#{k}\" value=\"#{v[0]}\">\n"
  }
  per.merge!({"ps"=>v})
return h
end
#idから個別検索
def field_search_a(key,db)
  sql = <<SQL
SELECT *
FROM            question
JOIN answer
ON question.id = answer.id
JOIN category
ON answer.id = category.id
JOIN image
ON category.id = images.id
WHERE question.id = #{key}
SQL
  data=[]
  db.query(sql).each{|row|
    data<<row
  }
  return data
end
#gazou取得
def bibimage(id,per)
  img="<img src=\'../meyasu_data/data#{printf('%03d',id)}.png\' width='200' height='287' alt='404'/>"
  return img
end
